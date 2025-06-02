import { Router } from '@angular/router';
import { SocialService } from './../../010_core/service/social.service';
import { Component } from '@angular/core';

@Component({
  selector: 'app-navbar',
  standalone: false,
  templateUrl: './navbar.component.html',
})
export class NavbarComponent {
  public loged: boolean = false;
  public user: any;
  public userName: string = 'undefined';
  constructor(
    private socialService: SocialService,
    private router: Router
    ) {}

  onLogedChange(loged: boolean): void {
    if (loged) {
      this.router.navigate(['/users/auth']);
    } else {
      this.router.navigate(['/']);
    }
  }

  ngOnInit(): void {
    this.loged = this.socialService.isLoggedIn();
    if (this.loged) {
      this.user = this.socialService.getUser();
      console.log('USER', this.user);
    }
  }
}
