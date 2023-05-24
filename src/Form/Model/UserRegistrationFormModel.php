<?php 

namespace App\Form\Model;

use App\Validator\UniqueUser;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserRegistrationFormModel
{
    #[NotBlank(message:'Поле не должно быть пустым')]
    #[Email()]
    #[UniqueUser()]
    public $email;
    
    public $firstName;

    #[Length(min:6, minMessage:"Пароль должен быть длиной не менее 6-ти символов")]
    #[NotBlank()]
    public $plainPassword;    

}