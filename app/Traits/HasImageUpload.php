<?php

namespace App\Traits;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

trait HasImageUpload
{



    public function handleImageUpdate($input, $model, $folder = 'uploads')
    {

        $data = $input instanceof \Illuminate\Http\Request ? $input->all() : $input;
        // إذا في صورة قديمة واحنا جايين نغيرها
        if (request()->hasFile('image') && $model->image) {
            Storage::disk('public')->delete($model->image);
        }


        // تحديث باقي البيانات باستثناء الصورة
        $model->update(collect($data)->except(['image'])->toArray());



        // رفع صورة جديدة إذا في
        if (request()->hasFile('image')) {
            $image = request()->file('image');
            $fileName = Str::random(5) . '_' . ($image->getClientOriginalName());
            $path = $image->storeAs($folder, $fileName, 'public');
            $model->image = $path;
        }

        $model->save();
        return $model;
    }

    public function handleImageCreation($input, $modelClass, $folder = 'uploads')
    {
        // إذا كان $input عبارة عن Request، حوله إلى array
        $data = $input instanceof \Illuminate\Http\Request ? $input->all() : $input;

        // رفع صورة جديدة إذا في
        if (request()->hasFile('image')) {
            $image = request()->file('image');
            $fileName = Str::random(5) . '_' . $image->getClientOriginalName();
            $path = $image->storeAs($folder, $fileName, 'public');
            $data['image'] = $path;
        }



        // إنشاء السجل الجديد
        return $modelClass::create($data);
    }
}
