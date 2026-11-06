# Book Review

A book review web application built with **Laravel 12**. Browse a catalogue of books, search by title, filter by popularity or rating, read reviews, and submit your own review with a star rating.

## Features

- **Browse books** with each book's author, average star rating, and review count
- **Search** books by title (partial match)
- **Filter** the list by:
  - Latest (default)
  - Popular last month
  - Highest rated last month
  - Popular last six months
  - Highest rated last six months
- **Book detail page** showing the average rating and every review for the book
- **Add a review** (review text + 1-5 rating) for any book
- **Rate limiting** on review submission: 3 reviews per hour per user/IP, with a friendly "Slow down!" page when the limit is hit
- **Caching** of book listings and book detail pages (1 hour), with automatic invalidation when a book's reviews change
- Reusable `<x-star-rating>` Blade component that renders ★/☆ stars from a rating

## Tech Stack

- **Backend:** PHP 8.2+, Laravel 12
- **Database:** SQLite by default (configurable via `.env`)
- **Cache / sessions / queue:** database drivers (default `.env.example` values)
- **Frontend:** Blade templates styled with Tailwind CSS (loaded via CDN in the layout)
- **Build tool:** Vite
- **Testing:** PHPUnit

## How It Works

1. `/` redirects to the books list (`books.index`).
2. The list reads the optional `title` and `filter` query parameters. A title search narrows the results, and the filter picks a scope:
   - `popular_last_month` / `popular_last_six_months` - books ordered by number of reviews in the period, then by rating
   - `highest_rated_last_month` / `highest_rated_last_six_months` - books ordered by average rating in the period, then by review count
   - To keep results meaningful, the monthly filters require **more than 2 reviews** and the six-month filters require **more than 5 reviews**
   - No filter - latest books first, with review count and average rating
3. Results are cached for one hour under a key built from the filter and title.
4. Opening a book loads it with its reviews, review count, and average rating (cached per book). Creating, updating, or deleting a review (or the book itself) clears that book's cached detail page.
5. "Add a review" opens a form for that book. Submitting validates that `review` and `rating` are present, saves the review against the book, and redirects back to the book page. This route is protected by the `reviews` rate limiter (3 per hour).

## Data Model

**books**

| Column       | Type   | Notes    |
|--------------|--------|----------|
| `id`         | id     | Primary key |
| `title`      | string |          |
| `author`     | string |          |
| `created_at` / `updated_at` | timestamps | |

**reviews**

| Column       | Type             | Notes                                   |
|--------------|------------------|------------------------------------------|
| `id`         | id               | Primary key                             |
| `book_id`    | foreign key      | References `books.id`, cascades on delete |
| `review`     | text             |                                          |
| `rating`     | unsigned tinyint | Rating given to the book (1-5 in the UI) |
| `created_at` / `updated_at` | timestamps | |

A `Book` has many `Review`s; a `Review` belongs to a `Book`.

## Routes

| Method | URI                        | Name                  | Description                                  |
|--------|----------------------------|-----------------------|-----------------------------------------------|
| GET    | `/`                        | -                     | Redirects to the books list                  |
| GET    | `/books`                   | `books.index`         | List books (supports `title` and `filter`)   |
| GET    | `/books/{book}`            | `books.show`          | Show a book and its reviews                  |
| GET    | `/books/{book}/reviews/create` | `books.reviews.create` | Show the add-review form                 |
| POST   | `/books/{book}/reviews`    | `books.reviews.store` | Save a review (throttled: 3 per hour)        |

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm

### Installation

```bash
git clone <your-repo-url> book-review
cd book-review
composer install
npm install
cp .env.example .env
php artisan key:generate
```

The app uses SQLite by default. Create the database file, run the migrations, and seed sample data:

```bash
touch database/database.sqlite
php artisan migrate --seed
```

The seeder creates 90 books, each with 5-30 reviews: 30 books with good ratings (4-5), 30 with mixed ratings (2-5), and 30 with poor ratings (1-3). Review dates are spread across the last two years so the "last month" and "last six months" filters have data to work with.

To use MySQL/PostgreSQL instead, update `DB_CONNECTION` and the related `DB_*` variables in `.env` before migrating.

### Running the app

```bash
composer run dev
```

This starts the Laravel dev server, queue listener, log watcher (Pail), and the Vite dev server together. Alternatively:

```bash
php artisan serve
```

Then open `http://localhost:8000` in your browser. (The layout loads Tailwind from a CDN, so the page styles work without running Vite, but need an internet connection.)

### Running tests

```bash
composer run test
```

## Project Structure

```
app/
  Http/Controllers/
    BookController.php          # Book list (search, filters, caching) and detail page
    ReviewsController.php       # Add-review form and review storage
  Models/
    Book.php                    # Query scopes (title, popular, highestRated, ...) + cache invalidation
    Review.php                  # Belongs to Book + cache invalidation
  Providers/AppServiceProvider.php  # Defines the "reviews" rate limiter
  View/Components/StarRating.php    # <x-star-rating> component
database/
  factories/                    # BookFactory, ReviewFactory (good / average / bad states)
  migrations/                   # books and reviews tables
  seeders/DatabaseSeeder.php    # Seeds 90 books with reviews
resources/views/
  layouts/app.blade.php         # Base layout and Tailwind component classes
  books/index.blade.php         # Search, filter tabs, book list
  books/show.blade.php          # Book detail and reviews
  books/reviews/create.blade.php # Add-review form
  components/star-rating.blade.php
  errors/429.blade.php          # "Slow down!" rate-limit page
routes/web.php                  # Application routes
```
