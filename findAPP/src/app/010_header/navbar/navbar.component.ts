import { Router } from '@angular/router';
import { SocialService } from './../../010_core/service/social.service';
import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-navbar',
  standalone: false,
  templateUrl: './navbar.component.html',
})
export class NavbarComponent {
  @Input() loged: boolean = false;
  @Input() user: any;
  public userName: string = 'undefined';
}
