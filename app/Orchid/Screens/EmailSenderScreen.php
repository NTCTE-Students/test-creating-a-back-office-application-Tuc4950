<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Alert;

class EmailSenderScreen extends Screen
{
     /**
       * Метод, определяющий все входные данные экрана. 
       * Именно в нём должны вызываться запросы к базе данных,
       * api или любые другие (не обязательно явно),
       * результатом которого должен быть массив, 
       * обращение к которым будут использоваться его ключи.
       */
    public function query(): array
    {
        return [
            'subject' => date('F').' Campaign News',
        ];
    }

    /**
     * Имя отображается на экране пользователя и в заголовках
     */
    public function name(): ?string
    {
        return "Email sender";
    }

    /**
     * Описание отображается на экране пользователя и в заголовках
     */
    public function description(): ?string
    {
        return "Tool that sends ad-hoc email messages.";
    }
    
     /**
       * Определяет управляющие кнопки и события,
       * которые должны будут произойти по нажатию
       */
    public function commandBar(): array
    {
        return [
            Button::make('Send Message')
                ->icon('paper-plane')
                ->method('sendMessage')
        ];
    }

    /**
 * Views.
 *
 * @return Layout[]
 */
public function layout(): array
{
    return [
        Layout::rows([
            Input::make('subject')
                ->title('Subject')
                ->required()
                ->placeholder('Message subject line')
                ->help('Enter the subject line for your message'),

            Relation::make('users.')
                ->title('Recipients')
                ->multiple()
                ->required()
                ->placeholder('Email addresses')
                ->help('Enter the users that you would like to send this message to.')
                ->fromModel(User::class,'name','email'),

            Quill::make('content')
                ->title('Content')
                ->required()
                ->placeholder('Insert text here ...')
                ->help('Add the content for the message that you would like to send.')

        ])
    ];
}
  /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'subject' => 'required|min:6|max:50',
            'users'   => 'required',
            'content' => 'required|min:10'
        ]);

        Mail::raw($request->get('content'), function (Message $message) use ($request) {
            $message->from('sample@email.com');
            $message->subject($request->get('subject'));

            foreach ($request->get('users') as $email) {
                $message->to($email);
            }
        });


        Alert::info('Your email message has been sent successfully.');
    }
}

