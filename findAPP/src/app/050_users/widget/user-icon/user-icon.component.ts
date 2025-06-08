import { Component, EventEmitter, Input, Output } from '@angular/core';
import { FindService } from '../../../010_core/service/find.service';
import { Router } from '@angular/router';
import { SocialService } from '../../../010_core/service/social.service';

@Component({
  selector: 'app-user-icon',
  standalone: false,
  templateUrl: './user-icon.component.html',
  styleUrl: './user-icon.component.scss',
})
export class UserIconComponent {
  img: string = 'assets/icons/login.svg';
  @Input() user: any;
  @Input() loged: boolean = false;
  @Output() logedChange = new EventEmitter<boolean>();


  constructor(
    private findService: FindService,
    private socialService: SocialService,
    private router: Router) {}

  login(): void {
    this.router.navigate(['/users/auth']);
  }

  profile(): void {
    this.router.navigate(['/users/details/'+this.user?.ID]);
  }
}
