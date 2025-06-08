import { Component } from '@angular/core';
import { SocialService } from '../../../010_core/service/social.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-user-logout',
  standalone: false,
  templateUrl: './user-logout.component.html',
  styleUrl: './user-logout.component.scss',
})
export class UserLogoutComponent {
  constructor(private socialService: SocialService, private router: Router) {}
  logout(): void {
    this.socialService.logout();
    this.router.navigate(['/']);
  }
}
