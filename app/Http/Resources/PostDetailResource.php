<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'post_title' => $this->post_title,
            'image' => $this->image,
            'content' => $this->content,
            'redditor_id' => $this->redditor,
            'comments_total' => $this->whenLoaded('comments', function(){
                return count($this->comments);
            }),
            'comments' => $this->whenLoaded('comments', function(){
                return collect($this->comments)->each(function($comment){
                    $comment->commenter;
                    return $comment;
                });
            }),
            'upvotes_total' => $this->whenLoaded('votes', function(){
                return count($this->votes);
            }),
            'upvotes' => $this->whenLoaded('votes', function(){
                return collect($this->votes)->each(function($upvotes){
                    $upvotes->supvotes;
                    return $upvotes;
                });
            }),
            'created_at' => $this->created_at,
        ];

    }
}