import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { SocialService } from '../../service/Api/social.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent {

  constructor(private fb: FormBuilder, private SocialService:SocialService) {
  }

  public user: Array<string> = [];

  ngOnInit() { }

  public userForm: FormGroup = this.fb.group(
    {
      user_login: ['',[Validators.required, Validators.minLength(2), Validators.maxLength(30)]]
    }
  )

  showLogin() { }
  showSignup() { }
  showForgotPassword() { }
  showSubscribe() { }
  showContactUs() { }

  signIn() {
    alert(this.user_login);
    alert("LOGIN");
    }

  public label_user_login='Login';
  public user_login='';
  public label_social_login = '';
  public label_social_sign_up = 'Cadastrar-se';
  public label_social_forgot_password = 'Esqueci minha senha';
  public label_social_subscrime = '';
  public label_social_contact_us = 'Contato';
  public label_social_message = '';
  public label_social_message_inf = '';
  public label_return = 'Voltar';
  public label_social_sign_in = 'Acessar';
  public label_social_type_login = 'Usuário';
  public label_user_password = '';
  public label_social_type_password = 'Senha';
  public label_social_not_user = 'Usuário não existe';
  public label_social_questions = 'FAQ';
  public label_social_forgot_password_info = '';
  public label_social_alread_user = 'Usuário já existe';
  public label_user_name = 'Nome do usuário';
  public label_signup_email = 'E-mail';
  public label_signup_institution = 'Instituição';
  public label_social_name = 'Nome completo';
  public label_social_yourmessage = 'Sua mensagem';
  public label_conecting = 'Conectando';
  public social_enter = 'Acessar';
  public social_social_sign_up = 'Cadastrar-se';
  public social_social_sign_in = 'Logar-se';
  public social_signup = 'Cadastrar-se';

}
