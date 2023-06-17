import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http'
import { Observable } from 'rxjs';
import { environment } from 'environments/environment';

@Injectable({
  providedIn: 'root'
})
export class SocialsApiService {

  private URL: string = `${environment.HTTP}`;
  constructor(private http: HttpClient) { }

  public signin(login: string, pass: string)
  {
    let url = `${this.URL}socials/signin?user={$login}&pwd=teste`;
    console.log(url);

    return this.http.get(url).subscribe({
      next: (res) => console.log(res),
      error: (err) => console.log(err),
    });
  }
}
