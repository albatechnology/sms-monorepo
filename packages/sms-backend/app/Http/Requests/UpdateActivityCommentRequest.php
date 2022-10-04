<?php

namespace App\Http\Requests;

use App\Models\ActivityComment;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateActivityCommentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('activity_comment_edit');
    }

    public function rules()
    {
        return [];
    }
}
