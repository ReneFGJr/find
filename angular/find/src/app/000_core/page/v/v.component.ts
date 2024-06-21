import { Component, Input } from '@angular/core';
import { FindService } from '../../service/find.service';
import { ActivatedRoute, Params, Router } from '@angular/router';

@Component({
  selector: 'app-v',
  templateUrl: './v.component.html',
})
export class VComponent {
  public data: Array<any> | any;
  public id: string = '';
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
      this.id = res['id'];
      let dt: Array<any> | any = { isbn: this.id, lib: this.lib };
      this.findService.api_post('getPubID/' + this.id, dt).subscribe((res) => {
        this.data = res;
      });

      console.log(res);
    });
  }
}
