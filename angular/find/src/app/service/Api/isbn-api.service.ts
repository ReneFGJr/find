import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http'
import { Observable } from 'rxjs';
import { environment } from 'environments/environment';

@Injectable({
  providedIn: 'root'
})
export class IsbnApiService {

  private URL:string = `${environment.HTTP}`;
  constructor(private http: HttpClient) { }

  public validISBN(isbn:string) : Observable<any>
    {
      let url=`${this.URL}isbn/`+isbn;
      return this.http.get(url);
    }
}
