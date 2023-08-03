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

  private url: string = 'https://cip.brapci.inf.br/api/';
  //private url: string = 'http://brp/api/';

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
    console.log('getLibrary');
    if (this.CookieService.check('library')) {
      let lib = this.CookieService.get('library');
      console.log('=lib==>', lib)
      return String(lib)
    } else {
      return '0'
    }
  }

  public validISBN(isbn:string): Observable<Array<any>> {
    let url = `${this.url}isbn/`+isbn;
    var formData: any = new FormData();
    console.log('validSBN' + url);
    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      res => res,
      error => error
    );
  }

  public getISBN(isbn: string): Observable<Array<any>> {
    if (isbn == '') { isbn = 'ERROR'; }
    let url = `${this.url}isbn/` + isbn;
    var formData: any = new FormData();
    console.log('getISBN' + url);
    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      res => res,
      error => error
    );
  }

  public addISBN(isbn: string): Observable<Array<any>> {
    alert(isbn)
    let url = `${this.url}find/isbn/` + isbn+'/add';
    console.log('addISBN'+url);
    var formData: any = new FormData();

    formData.append('library', '1');
    formData.append('apikey', 'ff63a314d1ddd425517550f446e4175e');

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      res => res,
      error => error
    );
  }

  public saveData(isbn: string, field: string, value: string): Observable<Array<any>> {
    alert("+++"+isbn)
    let url = `${this.url}find/saveField/`;
    console.log('addISBN' + url);
    var formData: any = new FormData();

    formData.append('library', '1');
    formData.append('apikey', 'ff63a314d1ddd425517550f446e4175e');

    formData.append('item', );
    formData.append('field', field);
    formData.append('value', value);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      res => res,
      error => error
    );
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
