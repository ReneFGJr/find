import { Component } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { FindService } from 'src/app/000_core/service/find.service';
import { SocialService } from 'src/app/000_core/service/social.service';

@Component({
  selector: 'app-password',
  templateUrl: './password.component.html',
  styleUrls: ['../signin/signin.component.scss'],
})
export class PasswordComponent {
  public loginForm: FormGroup | any;
  public html: string = 'Insruções';
  public data: Array<any> | any;

  constructor(
    private findService: FindService,
    private socialService: SocialService
  ) {}

  ngOnInit(): void {
    this.loginForm = new FormGroup({
      pass1: new FormControl('', Validators.required),
      pass2: new FormControl('', Validators.required),
      key: new FormControl('', Validators.required),
      verb: new FormControl('password', Validators.required),
    });
  }

  onSubmit(): void {
    if (this.loginForm.valid) {
      console.log('Form Submitted', this.loginForm.value);

      this.findService
        .api_post('social', this.loginForm.value)
        .subscribe((res) => {
          this.data = res;
          this.html = this.data.user.html;
          console.log(this.data);
        });
    } else {
      console.log('Form is invalid');
    }
  }
}
