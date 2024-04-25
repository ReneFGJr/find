import { Component } from '@angular/core';
import { FindService } from 'src/app/000_core/service/find.service';
import { ActivatedRoute, Params, Router } from '@angular/router';

@Component({
  selector: 'app-library-edit',
  templateUrl: './library-edit.component.html'
})
export class LibraryEditComponent {
  public data: Array<any> | any;
  public library: string = '';

  constructor(
    private ActivatedRoute: ActivatedRoute,
    private findService: FindService,
    private router: Router
  ) {}

  ngOnInit() {
    this.ActivatedRoute.params.subscribe((res) => {
      this.library = res['id'];
      console.log(this.library);

      let dt: Array<any> | any = { id: this.library };
      this.findService
        .api_post('getLibrary/' + this.library, dt)
        .subscribe((res) => {
          this.data = res;
          console.log(this.data);
        });
    });
  }
}
