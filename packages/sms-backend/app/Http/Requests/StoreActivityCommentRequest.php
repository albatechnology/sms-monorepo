<?php

namespace App\Http\Requests;

use App\Models\ActivityComment;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreActivityCommentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('activity_comment_create');
    }

    public function rules()
    {
        return [];
    }
}
