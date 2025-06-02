import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { CookieService } from 'ngx-cookie-service';
import { LocalStorageService } from './local-storage.service';

@Injectable({
  providedIn: 'root',
})
export class FindService {
  http: any;
  constructor(
    private HttpClient: HttpClient,
    private localStorage: LocalStorageService,
    private cookieService: CookieService
  ) {}

  //private url: string = 'http://find/v2/api/';
  private url: string = 'http://find/api/';
  //private url: string = 'https://www.ufrgs.br/find/v2/api/';

  public api_post(
    type: string,
    dt: Record<string, any> = {},
    development: boolean = false
  ): Observable<Array<any>> {
    let url = `${this.url}` + type;
    var formData: any = new FormData();
    let apikey = this.cookieService.get('section');
    let library = this.getLibrary();
    formData.append('user', apikey);
    formData.append('library', library);

    for (const key in dt) {
      formData.append(key, dt[key]);
    }

    console.log('=URL==' + url, dt);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  formatDateString(dateStr: string): string {
    if (!dateStr || dateStr === '0' || dateStr.length !== 8) {
      return '—';
    }
    const ano = dateStr.substr(0, 4);
    const mes = dateStr.substr(4, 2);
    const dia = dateStr.substr(6, 2);
    return `${dia}/${mes}/${ano}`;
  }

  /******************************************************************** */
  public saveCover(isbn: string, file: string) {
    let url = `${this.url}find/cover/` + isbn + '/upload';
    console.log(url);
    var formData: any = new FormData();

    formData.append('library', this.getLibrary());
    formData.append('apikey', 'ff63a314d1ddd425517550f446e4175e');
    formData.append('data', file);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  /******************************************************************** */
  public setLibrary(id: string) {
    this.cookieService.set('library', id, 365);
  }

  public getLibrary(): string {
    console.log('getLibrary');
    let lib = this.localStorage.get('library');
    return String(lib);
  }

  public search(term: string, c: string): Observable<Array<any>> {
    let url = `${this.url}find/search/` + term + '/' + c;
    var formData: any = new FormData();
    console.log(url);
    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public validISBN(isbn: string): Observable<Array<any>> {
    let url = `${this.url}isbn/` + isbn;
    var formData: any = new FormData();
    console.log('validSBN' + url);
    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public getISBN(isbn: string): Observable<Array<any>> {
    if (isbn == '') {
      isbn = 'ERROR';
    }
    let url = `${this.url}find/isbn/` + isbn;
    var formData: any = new FormData();
    console.log(url);
    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  /********************************** Autentication */

  public createConcept(name: string, classe: string): Observable<Array<any>> {
    let url = `${this.url}find/concept/add`;
    console.log(url);
    var formData: any = new FormData();

    formData.append('library', '1');
    formData.append('apikey', 'ff63a314d1ddd425517550f446e4175e');
    formData.append('term', name);
    formData.append('class', classe);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public addISBN(isbn: string): Observable<Array<any>> {
    let url = `${this.url}find/isbn/` + isbn + '/add';
    console.log(url);
    var formData: any = new FormData();

    formData.append('library', '1');
    formData.append('apikey', 'ff63a314d1ddd425517550f446e4175e');

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public register_item(isbn: string, place: string, tombo: string) {
    let lib = this.getLibrary();
    let url = `${this.url}find/putItemLibrary`;
    console.log(url);
    var formData: any = new FormData();
    formData.append('library', lib);
    formData.append('isbn', isbn);
    formData.append('place', place);
    formData.append('tombo', tombo);
    formData.append('apikey', 'ff63a314d1ddd425517550f446e4175e');

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public getPlace(): Observable<Array<any>> {
    let lib = this.getLibrary();
    let url = `${this.url}find/getPlace/` + lib;
    console.log(url);
    var formData: any = new FormData();
    formData.append('library', lib);
    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public saveData(
    isbn: string,
    field: string,
    value: string
  ): Observable<Array<any>> {
    let url = `${this.url}find/saveField`;
    console.log(url);

    var formData: any = new FormData();

    formData.append('library', '1');
    formData.append('apikey', 'ff63a314d1ddd425517550f446e4175e');

    formData.append('isbn', isbn);
    formData.append('field', field);
    formData.append('value', value);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public saveRDF(
    r1: string,
    prop: string,
    r2: string,
    lit: string
  ): Observable<Array<any>> {
    let url = `${this.url}find/saveRDF`;
    console.log(url);

    var formData: any = new FormData();

    formData.append('library', '1');
    formData.append('apikey', 'ff63a314d1ddd425517550f446e4175e');

    formData.append('r1', r1);
    formData.append('p', prop);
    formData.append('r2', r2);
    formData.append('literal', lit);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  /******************************************************************** */
  public libraries(): Observable<Array<any>> {
    let url = `${this.url}find/libraries/all`;
    console.log(`Bibliotecas: ${url}`);
    var formData: any = new FormData();

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }
}
