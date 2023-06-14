import { Component } from '@angular/core';
import { CookieService } from 'ngx-cookie-service';

@Component({
  selector: 'app-home-index',
  templateUrl: './home-index.component.html',
  styleUrls: ['./home-index.component.scss']
})
export class HomeIndexComponent {

  public library:string = '';

  constructor(private CookieService: CookieService) {}

  public ngOnInit(): void
    {
      if (this.CookieService.check('library'))
        {
          this.CookieService.set('library','1');
          console.log(this.CookieService.getAll());
        } else {

        }
    }

}
