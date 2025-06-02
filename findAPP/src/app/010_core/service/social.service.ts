import { CookieService } from 'ngx-cookie-service';
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { FindService } from './find.service';

@Injectable({
  providedIn: 'root',
})
export class SocialService {
  http: any;
  public user: Array<any> | any;
  public data: Array<any> | any;
  public message: string = '';

  private url: string = 'http://find/v2/api/';
  //private url: string = 'https://www.ufrgs.br/find/v2/api/';

  constructor(
    private HttpClient: HttpClient,
    private cookieService: CookieService,
    private findService: FindService
  ) {}

  public isLoggedIn(): boolean {
    {
      let apikey = this.cookieService.check('apiKey');

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
    let apikey = this.cookieService.get('section');
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
    return this.cookieService.check('apiKey');
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
    this.cookieService.set('apiKey', apiKey, 365);
    this.cookieService.set('fullName', fullname, 365);
    this.cookieService.set('nickname', nickname, 365);
    this.cookieService.set('email', email, 365);
    this.cookieService.set('id', idU, 365);
    this.cookieService.set('perfil', perfil, 365);
  }

  public logout() {
    this.cookieService.delete('apiKey', '');
    this.cookieService.delete('fullName', '');
    this.cookieService.delete('nickname', '');
    this.cookieService.delete('email', '');
    this.cookieService.delete('id', '');
    this.cookieService.delete('perfil', '');
  }

  public signIn(email: string, password: string): Observable<Array<any>> {
    let payload = {
      username: email,
      password: password,
      verb: 'signin',
    };

    let url = 'social/signin';
    this.findService.api_post(url, payload).subscribe(
      (res) => {
        this.data = res;
        let status = this.data?.user?.status;

        if (status == '200') {
          this.user = this.data['user'];
          this.setUser(
            this.user?.apiKey,
            this.user?.fullname,
            this.user?.email,
            this.user?.id,
            this.user?.nickname,
            this.user?.perfil
          );
          console.log("DADOS",this.data);
          console.log('APIKEY', this.user?.apiKey);
          this.data = {'status': '200','message': 'Usuário logado com sucesso!'};
        } else if (status == '400') {
          let message = this.data?.user?.message;
          this.message = message;
          this.data = {
            status: '400',
            message: 'e-mail ou senha inválidos',
          };
        }

      },
      (error) => {
        console.log(error);
      }
    );
    return this.data;
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
    console.log('getUser');
    if (this.cookieService.check('library')) {
      let ID = this.cookieService.get('id');
      let key = this.cookieService.get('apiKey');
      let fullname = this.cookieService.get('fullname');
      let nickname = this.cookieService.get('nickname');
      let email = this.cookieService.get('email');
      let perfil = this.cookieService.get('perfil');

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
