<?php

namespace App\Http\Requests;

use App\Models\SnipeModel;
use enshrined\svgSanitize\Sanitizer;
use Intervention\Image\Facades\Image;
use App\Http\Traits\ConvertsBase64ToFiles;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Exception\NotReadableException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ImageComponentUploadRequest extends Request
{
    use ConvertsBase64ToFiles;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
       
            return [
                'image.*' => 'mimes:png,gif,jpg,jpeg,svg,bmp,svg+xml,webp,avif',
                'avatar' => 'mimes:png,gif,jpg,jpeg,svg,bmp,svg+xml,webp,avif',
                'favicon' => 'mimes:png,gif,jpg,jpeg,svg,bmp,svg+xml,webp,image/x-icon,image/vnd.microsoft.icon,ico',
            ];
    }

    public function response(array $errors)
    {
        return $this->redirector->back()->withInput()->withErrors($errors, $this->errorBag);
    }
    
    /** 
     * Fields that should be traited from base64 to files
     */
    protected function base64FileKeys(): array
    {
        /**
         * image_source is here just legacy reasons. Api\AssetController
         * had it once to allow encoded image uploads.
        */ 
        return [
            'image' => 'auto',
            'image_source' => 'auto'
        ];
    }

    /**
     * Handle and store any images attached to request
     * @param SnipeModel $item Item the image is associated with
     * @param string $path  location for uploaded images, defaults to uploads/plural of item type.
     * @return SnipeModel        Target asset is being checked out to.
     */
    public function handleImages($item, $w = 600, $form_fieldname = 'image', $path = null, $db_fieldname = 'image')
    {
        $type = strtolower(class_basename(get_class($item)));

        if (is_null($path)) {
            $path = str_plural($type);

            if ($type == 'assetmodel') {
                $path = 'models';
            }

            if ($type == 'user') {
                $path = 'avatars';
            }
        }

        $uploaded_files = $this->file($form_fieldname);

        if (!is_array($uploaded_files)) {
            $uploaded_files = [$uploaded_files];
        }

        $file_names = [];

        foreach ($uploaded_files as $image) {
            if ($image instanceof UploadedFile) {
                $ext = $image->guessExtension();
                $file_name = $type.'-'.$form_fieldname.'-'.$item->id.'-'.str_random(10).'.'.$ext;

                if (in_array($image->getMimeType(), ['image/vnd.microsoft.icon', 'image/x-icon', 'image/avif', 'image/webp'])) {
                    // Просто сохранить без изменений
                    Storage::disk('public')->put($path.'/'.$file_name, file_get_contents($image));
                } elseif ($image->getMimeType() == 'image/svg+xml') {
                    $sanitizer = new Sanitizer();
                    $dirtySVG = file_get_contents($image->getRealPath());
                    $cleanSVG = $sanitizer->sanitize($dirtySVG);

                    try {
                        Storage::disk('public')->put($path . '/' . $file_name, $cleanSVG);
                    } catch (\Exception $e) {
                        Log::debug($e);
                    }
                } else {
                    try {
                        $upload = Image::make($image->getRealPath())
                            ->setFileInfoFromPath($image->getRealPath())
                            ->resize(null, $w, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            })->orientate();

                        Storage::disk('public')->put($path.'/'.$file_name, (string) $upload->encode());
                    } catch (NotReadableException $e) {
                        Log::debug($e);
                        $validator = Validator::make([], []);
                        $validator->errors()->add($form_fieldname, trans('general.unaccepted_image_type', ['mimetype' => $image->getClientMimeType()]));

                        throw new \Illuminate\Validation\ValidationException($validator);
                    }
                }

                $file_names[] = $file_name;
            }
        }

        if (!empty($file_names)) {
            // Обновление модели
            if(!empty($item->{$db_fieldname})){
                $file_names = array_merge(json_decode($item->{$db_fieldname}), $file_names);
            }
            $item->{$db_fieldname} = json_encode($file_names); // Сохраняем имена файлов как JSON
            $item->save();
        }
        if ($this->input('image_delete')) {
            $item = $this->deleteExistingImage($item, $path, $db_fieldname, $this->input('image_delete'));
            $item->save();
        }
        return $item;
    }


    public function deleteExistingImage($item, $path = null, $db_fieldname = 'image', $images = [])
    {
        if ($item->{$db_fieldname}) {
            $file_names = json_decode($item->{$db_fieldname});
            //die(json_encode($file_names));
            foreach ($file_names as $key => $file_name) {
                if(in_array($file_name, $images)){
                    
                    try {
                        unset($file_names[$key]);
                        Storage::disk('public')->delete($path.'/'.$file_name);
                        
                        
                    } catch (\Exception $e) {
                        Log::debug($e);
                    }
                }
                
            }
            //die(json_encode($file_names));
            $file_names = array_values($file_names);
            $item->{$db_fieldname} = json_encode($file_names);
        }

        return $item;
    }

    
}
