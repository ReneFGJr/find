import { Observable } from 'rxjs';
import { Component } from '@angular/core';
import { FindService } from '../../service/find.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-welcome',
  templateUrl: './welcome.component.html',
})
export class WelcomeComponent {
  constructor(
    private findService: FindService,
    private route: ActivatedRoute,
    private router: Router
  ) {}

  public lib: string = '';
  public bookRow: Array<any> | any;
  public total: number = 0;
  public logo: string = 'assets/img/logo/logo_find.png'

  ngOnInit() {
    let lib = this.findService.getLibrary();
    if (lib === '' || lib === '0') {
      this.router.navigate(['library']);
    }
    this.lib = lib;
    /* Logo */
    this.logo = 'http://ufrgs.br/find/img/logo/logo_'+this.lib+'.jpg';

    let dt = [[]];
    this.findService.api_post('vitrine/' + lib, dt).subscribe((res) => {
      this.bookRow = res;
      this.total = this.bookRow.items.length;
    });
  }

  resultSearch($e: Array<any>) {
    this.bookRow = $e;
  }
}
