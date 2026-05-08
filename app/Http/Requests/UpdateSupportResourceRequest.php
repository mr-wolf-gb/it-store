<?php

namespace App\Http\Requests;

use App\Models\SupportResource;
use App\Rules\InternalOrExternalLink;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupportResourceRequest extends FormRequest
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
        $maxFileKb = max((int) config('uploads.max_file_kb', 102400), 1);
        $allowedExtensions = config('uploads.allowed_extensions', ['zip', '7z', 'rar', 'pdf', 'txt', 'md', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'msi', 'exe', 'bat', 'ps1', 'sh', 'json', 'xml', 'csv', 'log', 'iso']);
        $mimesRule = 'mimes:'.implode(',', $allowedExtensions);
        $extensionsRule = 'extensions:'.implode(',', $allowedExtensions);

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('support_resources', 'slug')->ignore($this->route('supportResource'))],
            'resource_type' => ['required', Rule::in(SupportResource::TYPE_OPTIONS)],
            'visibility' => ['required', Rule::in(SupportResource::VISIBILITY_OPTIONS)],
            'status' => ['required', Rule::in(SupportResource::STATUS_OPTIONS)],
            'version' => ['nullable', 'string', 'max:50'],
            'changelog' => ['nullable', 'string', 'max:10000'],
            'category_id' => ['nullable', 'integer', Rule::exists('resource_categories', 'id')],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', Rule::exists('resource_tags', 'id')],
            'is_featured' => ['nullable', 'boolean'],
            'source_type' => ['required', Rule::in(SupportResource::SOURCE_OPTIONS)],
            'upload_files' => ['nullable', 'array'],
            'upload_files.*' => ['file', $mimesRule, $extensionsRule, 'max:'.$maxFileKb],
            'remove_file_ids' => ['nullable', 'array'],
            'remove_file_ids.*' => ['integer', Rule::exists('support_resource_files', 'id')],
            'link_url' => ['exclude_unless:source_type,link', 'required', 'string', 'max:2048', new InternalOrExternalLink],
        ];
    }
}
