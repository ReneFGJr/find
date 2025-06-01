import { Component } from '@angular/core';

@Component({
  selector: 'app-users',
  standalone: false,
  templateUrl: './users.component.html',
  styleUrl: './users.component.scss'
})
export class UsersComponent {
  public act: string = 'list';

  ngOnInit()
    {
    console.log('UsersComponent ngOnInit');
    }
}
