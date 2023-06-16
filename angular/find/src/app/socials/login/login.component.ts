import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { SocialService } from '../../service/Api/social.service';
import { trigger, state, style, animate, transition, keyframes } from '@angular/animations';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss'],
})

export class LoginComponent {
  public rec_prism = "color: red";
  public condition:number = 1;
  public erro = "OK";

  constructor(private fb: FormBuilder, private SocialService:SocialService) {
  }

  public user: Array<string> = [];
  user_password = 'xx';

  ngOnInit() { }

  public userForm: FormGroup = this.fb.group(
    {
      user_login: ['',[Validators.required, Validators.minLength(2), Validators.maxLength(30)]],
      user_password: ['',[Validators.required, Validators.minLength(2), Validators.maxLength(30)]]
    }
  )

  showLogin() {
          alert("Login");
           }
  showSignup() { alert("showSignup"); }
  showForgotPassword()
    {
      //this.rec_prism = "translateZ(-200px) rotateY( -180deg)";
      this.rec_prism = "translateZ(-200px) rotateY( -180deg)";
    }
  showSubscribe() {  alert("showSubscribe"); }
  showContactUs() {  alert("showContactUs"); }

  signIn() {
    let user_login = this.userForm.get('user_login')?.value;
    let user_password = this.userForm.get('user_password')?.value;
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
