import { Component } from '@angular/core';
import { FindService } from '../../service/find.service';
import { ActivatedRoute, Params, Router } from '@angular/router';

@Component({
  selector: 'app-libraries',
  templateUrl: './libraries.component.html',
  styleUrls: ['./libraries.component.scss']
})
export class LibrariesComponent {
  public id: string = '';
  public libraries: Array<any> | any;
  constructor(
    private findService: FindService,
    private route: ActivatedRoute,
    private router: Router
  ) { }

  ngOnInit() {
    let lib = this.findService.getLibrary();
    this.route.params.subscribe(
      (params: Params) => {
        this.id = String(+params["id"]);
        if (this.id !== '0' && this.id !== 'NaN')
          {
              this.findService.setLibrary(this.id);
               console.log('BIBLIOTECA==>' + this.id);
               this.router.navigate(['/'])
          } else {
            console.log('select')
          }
      }
    );


    this.findService.libraries().subscribe(
      res => {
        this.libraries = res;
        this.libraries = this.libraries.data
      }
    );
  }

}
