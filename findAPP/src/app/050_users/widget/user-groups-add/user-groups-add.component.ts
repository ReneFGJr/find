import { Component, Input } from '@angular/core';
import { Router } from '@angular/router';
import { FindService } from '../../../010_core/service/find.service';
import { LocalStorageService } from '../../../010_core/service/local-storage.service';

@Component({
  selector: 'app-user-groups-add',
  standalone: false,
  templateUrl: './user-groups-add.component.html',
  styleUrl: './user-groups-add.component.scss',
})
export class UserGroupsAddComponent {
  @Input() public idGroup: string = '';
  group: any;
  data: any;

  constructor(
    private routes: Router,
    private findService: FindService,
    private localStorage: LocalStorageService
  ) {}

  ngOnInit() {
    let library = this.localStorage.get('library');
    this.findService.api_post('admin/group/' + this.idGroup).subscribe({
      next: (response) => {
        this.group = response;
        console.log(response);
      },
    });
  }
}
