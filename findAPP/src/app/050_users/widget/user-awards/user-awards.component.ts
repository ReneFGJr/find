import { Component } from '@angular/core';

@Component({
  selector: 'app-user-awards',
  standalone: false,
  templateUrl: './user-awards.component.html',
  styleUrl: './user-awards.component.scss'
})
export class UserAwardsComponent {
  awards: Array<{ name: string; description: string; icon: string }> = [
    { name: 'Award 1', description: 'Description for Award 1', icon: 'icon1.png' },
    { name: 'Award 2', description: 'Description for Award 2', icon: 'icon2.png' },
    { name: 'Award 3', description: 'Description for Award 3', icon: 'icon3.png' },
  ]
}
