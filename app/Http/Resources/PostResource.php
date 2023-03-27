<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'upvotes_total' => $this->whenLoaded('votes', function(){
                return count($this->votes);
            }),
        ];

    }
}