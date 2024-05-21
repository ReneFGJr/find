import { Component } from '@angular/core';
import {
  FormGroup,
  FormBuilder,
  Validators,
  FormControl,
} from '@angular/forms';
import { FindService } from 'src/app/000_core/service/find.service';
import { SocialService } from 'src/app/000_core/service/social.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['../signin/signin.component.scss'],
})
export class LoginComponent {
  public loginForm: FormGroup | any;
  public data: Array<any> | any;

  constructor(
    private findService: FindService,
    private socialService: SocialService
  ) {}

  ngOnInit(): void {
    this.loginForm = new FormGroup({
      username: new FormControl('', Validators.required),
      password: new FormControl('', Validators.required),
      verb: new FormControl('signin', Validators.required),
    });
  }

  onSubmit(): void {
    if (this.loginForm.valid) {
      console.log('Login Submitted', this.loginForm.value);

      this.findService
        .api_post('social', this.loginForm.value)
        .subscribe((res) => {
          this.data = res;
          if (this.data.user.status == '200') {
            this.socialService.setUser(
              this.data.user.apikey,
              this.data.user.fullname,
              this.data.user.email,
              this.data.user.ID,
              this.data.user.nickname,
              this.data.user.perfil
            );
          }
          document.location.href = '/';
        });
    } else {
      console.log('Form is invalid');
    }
  }
}
