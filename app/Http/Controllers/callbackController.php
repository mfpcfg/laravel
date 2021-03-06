<?php

namespace App\Http\Controllers;

use App\Mail\mailSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Zttp\Zttp;

class callbackController extends Controller
{

    /* Відтворюю повністю сторінку "Обратная связь" */
	public function all()
	{

		return view('front.callback.index');
	}


    /* Метод, який відправляє форму "Обратная связь" і здійснює перевірку Google reCAPTCHA*/
    public function call_back(Request $request)
    {

        /* Параметри, які передаємо при відправкі форми */
        $name = $request->name;
    	$phone = $request->phone;
    	$email = $request->email;
    	$msg = $request->msg;
        
        /* Бібліотека Zttp компонує наші HTTP запроси при перевірці Google reCAPTCHA */
        $response = Zttp::asFormParams()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => '6LdSzmsUAAAAAB8ANm-oGfJ63P30l_5EeAEg8bz2',
            'response' => request('g-recaptcha-response'),
            'remoteip' => request()->ip(),
        ]);

        /* Перевірка на відмітку Google reCAPTCHA */
        if($response->json()['success'] !== true){

           
           return redirect()->back()->withFlashMessage('Извените! Ваше сообщение не было отправлено, Вам нужно поставить отметку в поле "Я не робот"');
 
        }

            Mail::to('mfpcfg@gmail.com')->send(new mailSetting($name, $phone, $email, $msg));
    	    return redirect()->back()->withFlashMessage("Спасибо! Ваше сообщение успешно отправлено. Наши менеджеры обязательно свяжутся с Вами и ответят на все Ваши вопросы.");
    		
    }
}
