import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http'
import { Observable } from 'rxjs';
import { environment } from 'environments/environment';
import { CookieService } from 'ngx-cookie-service';

@Injectable({
  providedIn: 'root'
})
export class LibraryApiService {

  private URL:string = `${environment.HTTP}`;
  constructor(private http: HttpClient, private Cookie: CookieService) { }

  public getLibraries() : Observable<any>
    {
      let url=`${this.URL}find/libraries`;
      return this.http.get(url);
    }

  public cookieExists(lib: string): boolean {
    if (this.Cookie.get(lib)) {
      return true;
    }
    return true;
  }
}
