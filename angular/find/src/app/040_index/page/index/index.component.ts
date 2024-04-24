import { Component } from '@angular/core';
import { FindService } from 'src/app/000_core/service/find.service';
import { ActivatedRoute, Params, Router } from '@angular/router';

@Component({
  selector: 'app-index',
  templateUrl: './index.component.html',
})
export class IndexComponent {
  public data: Array<any> | any;
  public type: string = '';
  public tipo: string = '';
  public lib: string = '';

  constructor(
    private ActivatedRoute: ActivatedRoute,
    private findService: FindService,
    private router: Router
  ) {}

  ngOnInit() {
    let lib = this.findService.getLibrary();
    if (lib === '' || lib === '0') {
      this.router.navigate(['library']);
    }
    this.lib = lib;

    this.ActivatedRoute.params.subscribe((res) => {
      this.type = res['type'];
      console.log(this.type);
      if (this.type == 'authors') {
        this.tipo = 'Autores';
      } else if (this.type == 'subject') {
        this.tipo = 'Assuntos';
      }

      let dt: Array<any> | any = { lib: this.lib };
      console.log(dt);
      this.findService
        .api_post('getIndex/' + this.type, dt)
        .subscribe((res) => {
          console.log(res);
          this.data = res;
        });

      console.log(res);
    });
  }
}
