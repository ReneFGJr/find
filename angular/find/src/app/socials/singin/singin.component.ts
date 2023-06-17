import { LocalStorageService } from './../../local-storage.service';
import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { SocialsApiService } from './../../socials/socials-api.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { environment } from 'environments/environment';

@Component({
  selector: 'app-singin',
  templateUrl: './singin.component.html',
  styleUrls: ['./singin.component.scss']
})

export class SinginComponent implements OnInit {
  public localItem = '';
  public statusForm = 'status';

  public userLoginForm: FormGroup = this.FormBuilder.group(
    {
      login: ['', [Validators.required, Validators.minLength(1), Validators.maxLength(100)]],
      pwd: ['', [Validators.required, Validators.minLength(1), Validators.maxLength(40)]]
    }
  );

  constructor(
    private FormBuilder: FormBuilder,
    private SocialsApiService: SocialsApiService,
    private http: HttpClient) { }
  ngOnInit(): void {

  }

  signIN(): void {

    if (this.userLoginForm.valid) {
      this.statusForm = 'Enviado';
      let user = this.userLoginForm.get('login')?.value;
      let pwd = this.userLoginForm.get('pwd')?.value;
      console.log('User: '+user);
      console.log('Pass: ' + pwd);

      let rsp = this.SocialsApiService.signin('teste','teste');
      console.log(rsp);


    }
  }
}
