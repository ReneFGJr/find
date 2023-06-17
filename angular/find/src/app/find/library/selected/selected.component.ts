import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { Library } from 'src/app/dataModel/library';
import { LibraryApiService } from 'src/app/service/Api/library-api.service';
import { CookieService } from 'ngx-cookie-service';

@Component({
  selector: 'app-selected',
  templateUrl: './selected.component.html',
  styleUrls: ['./selected.component.scss']
})

export class SelectedComponent implements OnInit {
  //_library: Library;

  constructor(
    private _Activatedroute: ActivatedRoute,
    private router: Router,
    private _libraryService: LibraryApiService,
    private Cookie: CookieService
  ) { }

  sub:any;

  ngOnInit() {
    this.sub = this._Activatedroute.paramMap.subscribe((params) => {
      let idx:string = params.get('id')!;
      console.log("=========>"+idx);
      this.Cookie.deleteAll();
      this.Cookie.set('lib-sel', idx);
      console.log(this.Cookie.getAll());
      this.router.navigate(['/main'])


    });
  }

  ngOnDestroy() {
    if (this.sub) this.sub.unsubscribe();
  }

}
