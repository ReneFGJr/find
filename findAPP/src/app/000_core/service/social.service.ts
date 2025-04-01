import { CookieService } from 'ngx-cookie-service';
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class SocialService {
  http: any;
  public user: Array<any> | any;

  constructor(
    private HttpClient: HttpClient,
    private cookieService: CookieService
  ) {}

  //private url: string = 'http://find/v2/api/';
  private url: string = 'https://www.ufrgs.br/find/v2/api/';

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
      this.user = {}
    }
    return this.user;
  }
}
