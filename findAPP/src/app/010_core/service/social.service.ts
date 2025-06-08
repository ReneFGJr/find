import { Router } from '@angular/router';
import { CookieService } from 'ngx-cookie-service';
import { EventEmitter, Injectable, Output } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { delay, map, Observable, tap } from 'rxjs';
import { FindService } from './find.service';
import { LocalStorageService } from './local-storage.service';

@Injectable({
  providedIn: 'root',
})
export class SocialService {
  http: any;
  @Output() Response: EventEmitter<any> = new EventEmitter();
  public user: Array<any> | any;
  public data: Array<any> | any;
  public message: string = '';

  private url: string = 'http://find/v2/api/';
  //private url: string = 'https://www.ufrgs.br/find/v2/api/';

  constructor(
    private HttpClient: HttpClient,
    private cookieService: CookieService,
    private findService: FindService,
    private localStorageService: LocalStorageService,
    private router: Router
  ) {}

  public isLoggedIn(): boolean {
    {
      let apikey = this.localStorageService.check('apiKey');

      if (apikey) {
        return true;
      } else {
        return false;
      }
    }
  }

  public api_post(type: string, dt: Array<any> = []): Observable<Array<any>> {
    let url = `${this.url}` + type;

    var formData: any = new FormData();
    let apikey = this.localStorageService.get('section');
    formData.append('user', apikey);

    for (const key in dt) {
      formData.append(key, dt[key]);
    }

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public loged() {
    return this.localStorageService.check('apiKey');
  }

  /******************************************************************** */
  public setUser(
    apiKey: string,
    fullname: string,
    email: string,
    idU: string,
    nickname: string,
    perfil: Array<any> | any
  ) {
    this.localStorageService.set('apiKey', apiKey);
    this.localStorageService.set('fullName', fullname);
    this.localStorageService.set('nickname', nickname);
    this.localStorageService.set('email', email);
    this.localStorageService.set('ID', idU);
    this.localStorageService.set('perfil', perfil);
    console.log("IDuser", idU);
  }

  public logout() {
    this.localStorageService.remove('apiKey');
    this.localStorageService.remove('fullName');
    this.localStorageService.remove('nickname');
    this.localStorageService.remove('email');
    this.localStorageService.remove('id');
    this.localStorageService.remove('perfil');
    // aguarda 1 segundo antes de navegar
  }

  // retire o subscribe interno e devolva o Observable:
  public signIn(email: string, password: string): Observable<any> {
    const payload = { username: email, password, verb: 'signin' };
    const url = 'social/signin';
    return this.findService.api_post(url, payload).pipe(
      tap((res) => {
        this.data = res;
        const status = this.data?.user?.status;
        if (status === '200') {
          const u = this.data.user;
          this.setUser(
            u.apikey,
            u.fullname,
            u.email,
            u.ID,
            u.nickname,
            u.perfil
          );
        }
      }),
      map((res) => {
        this.data = res;
        const status = this.data?.user?.status;
        if (status === '200') {
          return { status: '200', message: 'Usuário logado com sucesso!' };
        } else {
          return {
            status: '400',
            message: this.data.user?.message || 'Credenciais inválidas',
          };
        }
      })
    );
  }

  public forgotPassword(email: string): Array<any> {
    console.log('forgotPassword');
    return [];
  }

  public signUp(
    name: string,
    email: string,
    password: string,
    confirmPassword: string
  ): Array<any> {
    console.log('signUp');
    return [];
  }

  public getUser(): string {
    if (this.localStorageService.check('library')) {
      let ID = this.localStorageService.get('ID');
      let key = this.localStorageService.get('apiKey');
      let fullname = this.localStorageService.get('fullname');
      let nickname = this.localStorageService.get('nickname');
      let email = this.localStorageService.get('email');
      let perfil = this.localStorageService.get('perfil');

      this.user = {
        apikey: key,
        fullname: fullname,
        nickname: nickname,
        email: email,
        ID: ID,
        perfil: perfil,
      };
    } else {
      this.user = {};
    }
    return this.user;
  }
}
