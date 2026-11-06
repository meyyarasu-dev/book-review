<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Book extends Model
{
    use HasFactory;
   public function reviews(){
        return $this->hasMany(Review::class);
   }

   public function scopeTitle(Builder $query,string $title){
        return $query->where('title','LIKE','%' .$title. '%');
   }

   public function scopeWithReviewsCount(Builder $query,$from = null,$to = null): Builder|QueryBuilder{
        return $query->withCount([
            'reviews'=>fn(Builder $q) => $this->dateRangeFilter($q,$from,$to)
        ]);
   }

   public function scopeWithAverageRating(Builder $query,$from = null,$to = null): Builder|QueryBuilder{
        return $query->withAvg(['reviews'=>fn(Builder $q) => $this->dateRangeFilter($q,$from,$to)],'rating');
   }

   public function scopePopular(Builder $query,$from = null,$to = null): Builder|QueryBuilder{
        return $query->withCount([
            'reviews'=>fn(Builder $q) => $this->dateRangeFilter($q,$from,$to)
        ])->orderBy('reviews_count','desc');
   }

   public function scopeHighestRated(Builder $query,$from = null,$to = null): Builder|QueryBuilder{
        return $query->withAvg(['reviews'=>fn(Builder $q) => $this->dateRangeFilter($q,$from,$to)],'rating')->orderBy('reviews_avg_rating','desc');
   }

   public function scopeMinReviews(Builder $query,$minReviews=null){
        return $query->having('reviews_count','>',$minReviews);
   }

   private function dateRangeFilter(Builder $query,$from = null,$to = null){
        if($from && !$to){
            $query->where('created_at','>=',$from);
        }elseif(!$from && $to){
            $query->where('created_at','<=',$to);
        }
        elseif($from && $to){
            $query->whereBetween('created_at',[$from,$to]);
        }
   }

   public function scopePopularLastMonth(Builder $query):Builder|QueryBuilder {
        return $query->popular(now()->subMonth(),now())->highestrated()->minreviews(2);
   }

   public function scopePopularLastSixMonths(Builder $query):Builder|QueryBuilder {
        return $query->popular(now()->subMonths(6),now())->highestrated()->minreviews(5);
   }

   public function scopeHighestRatedLastMonth(Builder $query):Builder|QueryBuilder {
        return $query->highestrated()->popular(now()->subMonth(),now())->minreviews(2);
   }

   public function scopeHighestRatedLastSixMonths(Builder $query):Builder|QueryBuilder {
        return $query->highestrated()->popular(now()->subMonths(6),now())->minreviews(5);
   }

   public static function booted()
    {
        static::updated(fn(Book $book)=>cache()->forget('books:'. $book->id));
        static::deleted(fn(Book $book)=>cache()->forget('books:'. $book->id));
        static::created(fn(Book $book)=>cache()->forget('books:'. $book->id));
    }
}
