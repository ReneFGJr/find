import { Component } from '@angular/core';
import { SocialService } from 'src/app/000_core/service/social.service';

@Component({
  selector: 'app-logout',
  templateUrl: './logout.component.html',
  styleUrls: ['./logout.component.scss'],
})
export class LogoutComponent {
  constructor(
    private socialService: SocialService
  ) {}

  ngOnInit() {
    console.log('Logout');
    this.socialService.logout()
    document.location.href = '/';
  }
}
