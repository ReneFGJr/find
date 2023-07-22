import { CookieService } from 'ngx-cookie-service';
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class FindService {

  http: any;
  constructor(
    private HttpClient: HttpClient,
    private CookieService: CookieService,
  ) { }

  //private url: string = 'https://cip.brapci.inf.br/api/';
  private url: string = 'http://brp/api/';

  /******************************************************************** */
  public vitrine() {
    let lib = this.getLibrary();
    //if (lib !== '0')
    {
      let url = `${this.url}find/vitrine?library=` + lib;
      console.log(`Bibliotecas: ${url}`);
      var formData: any = new FormData();

      return this.HttpClient.post<Array<any>>(url, formData).pipe(
        res => res,
        error => error
      );
    }
  }

  /******************************************************************** */
  public setLibrary(id: string) {

    this.CookieService.set('library', id, 365);
  }

  public getLibrary(): string {

    if (this.CookieService.check('library')) {
      let lib = this.CookieService.get('library');
      console.log('=lib==>', lib)
      return String(lib)
    } else {
      return '0'
    }
  }

  /******************************************************************** */
  public libraries(): Observable<Array<any>> {
    let url = `${this.url}find/libraries/all`;
    console.log(`Bibliotecas: ${url}`);
    var formData: any = new FormData();

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      res => res,
      error => error
    );
  }

}
