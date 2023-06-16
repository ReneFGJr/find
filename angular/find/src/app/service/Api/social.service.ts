import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http'
import { Observable } from 'rxjs';
import { environment } from 'environments/environment';

@Injectable({
  providedIn: 'root'
})
export class SocialService {

  private URL:string = `${environment.HTTP}`;
  constructor(private http: HttpClient) { }

  public userLogin(user: string, pass: string) : Observable<any>
    {
      let url=`${this.URL}social/users`;
      return this.http.get(url);
    }
}