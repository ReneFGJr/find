import { FindService } from './../../../010_core/service/find.service';
import { Component } from '@angular/core';
import { LocalStorageService } from '../../../010_core/service/local-storage.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-user-groups',
  standalone: false,
  templateUrl: './user-groups.component.html',
  styleUrl: './user-groups.component.scss',
})
export class UserGroupsComponent {
  groups: any;
  constructor(
    private routes: Router,
    private findService: FindService,
    private localStorage: LocalStorageService
  ) {}

  ngOnInit() {
    let library = this.localStorage.get('library');
    this.findService.api_post('admin/groups/' + library).subscribe({
      next: (response) => {
        this.groups = response;
        console.log(response);
      },
    });
  }

  addUserToGroup(groupId: string) {
    this.routes.navigate(['/groups/addUserToGroup/' + groupId]);
    //window.location.href = '/groups/addUserToGroup/' + groupId;
  }

  profile(id: string) {
    this.routes.navigate(['/users/details/' + id]);
    //window.location.href = '/users/details/' + id;
  }
}
