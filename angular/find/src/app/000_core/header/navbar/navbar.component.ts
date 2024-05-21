import { Component } from '@angular/core';
import { FindService } from '../../service/find.service';
import { ActivatedRoute, Router } from '@angular/router';
import { SocialService } from '../../service/social.service';

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
})
export class NavbarComponent {
  public logo: string = 'assets/img/logo/logo_find_w.png'
  public loged: boolean = false
  public user: Array<any> | any

  constructor(
    private findService: FindService,
    private route: ActivatedRoute,
    private router: Router,
    private socialService: SocialService
  ) {}

  ngOnInit() {
    let lib = this.findService.getLibrary();

    this.loged = this.socialService.loged();

    if (this.loged === true) {
      this.user = this.socialService.getUser();
    }

    if (lib === '' || lib === '0') {
      this.router.navigate(['library']);
    }
    this.logo = 'assets/img/logo/logo_find_w.png';
    /* Logo */
    this.logo = 'http://ufrgs.br/find/img/logo/logo_' + lib + '.jpg';
  }
}
