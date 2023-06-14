import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http'
import { Observable } from 'rxjs';
import { environment } from 'environments/environment';

@Injectable({
  providedIn: 'root'
})
export class LibraryApiService {

  private URL:string = `${environment.HTTP}`;
  constructor(private http: HttpClient) { }

  public getLibraries() : Observable<any>
    {
      let url=`${this.URL}find/libraries`;
      return this.http.get(url);
    }
}
