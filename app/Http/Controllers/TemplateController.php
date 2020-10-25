<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function load(Request $request)
    {
        $templates = Template::get();
        return response()->json([
            'templates' => $templates
        ]);
    }

    public function save(Request $request)
    {
        if ($request['id']) {
            $template = Template::find($request['id']);
            $template->update($request->only('name','opts'));
            //$template->opts = $request['opts'];
        } else {
            $template = Template::create([
                'name' => $request['name'],
                'opts' => $request['opts']
            ]);
            $template->save();
        }

        return response()->json([
            'id' => $template->id
        ]);
    }

    public function delete(Request $request)
    {
        $template = Template::find($request['id']);
        if ($template) {
            $template->delete();
        }
        return response()->json([
            'id' => $request['id']
        ]);
    }
}
