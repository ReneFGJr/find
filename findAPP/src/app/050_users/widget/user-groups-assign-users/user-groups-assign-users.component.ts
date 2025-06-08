import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-user-groups-assign-users',
  standalone: false,
  templateUrl: './user-groups-assign-users.component.html',
  styleUrl: './user-groups-assign-users.component.scss'
})
export class UserGroupsAssignUsersComponent {
  @Input() public assignedUsers: any[] = [];
}
