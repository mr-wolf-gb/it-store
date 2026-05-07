<?php

namespace App\Http\Requests;

use App\Models\SupportResource;
use App\Rules\InternalOrExternalLink;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupportResourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('resources.manage') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'resource_type' => ['required', Rule::in(SupportResource::TYPE_OPTIONS)],
            'visibility' => ['required', Rule::in(SupportResource::VISIBILITY_OPTIONS)],
            'source_type' => ['required', Rule::in(SupportResource::SOURCE_OPTIONS)],
            'upload_file' => ['required_if:source_type,upload', 'file', 'max:204800'],
            'link_url' => ['required_if:source_type,link', 'string', 'max:2048', new InternalOrExternalLink],
        ];
    }
}
