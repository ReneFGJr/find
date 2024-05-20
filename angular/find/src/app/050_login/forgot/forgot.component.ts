import { Component } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { FindService } from 'src/app/000_core/service/find.service';

@Component({
  selector: 'app-forgot',
  templateUrl: './forgot.component.html',
  styleUrls: ['../signin/signin.component.scss'],
})
export class ForgotComponent {
  public loginForm: FormGroup | any;

  constructor(
    private findService: FindService,
  ) {}

  ngOnInit(): void {
    this.loginForm = new FormGroup({
      username: new FormControl('', Validators.required),
      verb: new FormControl('forgot', Validators.required),
    });
  }

  onSubmit(): void {
    if (this.loginForm.valid) {
      console.log('Form Submitted', this.loginForm.value);

      this.findService.api_post('login', this.loginForm.value).subscribe((res) => {
        console.log(res);
      });
    } else {
      console.log('Form is invalid');
    }
  }
}
