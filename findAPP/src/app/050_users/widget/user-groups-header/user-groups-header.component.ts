import { LocalStorageService } from './../../../010_core/service/local-storage.service';
import { Component, ElementRef, Input, ViewChild } from '@angular/core';
import { FindService } from '../../../010_core/service/find.service';

@Component({
  selector: 'app-user-groups-header',
  standalone: false,
  templateUrl: './user-groups-header.component.html',
  styleUrl: './user-groups-header.component.scss',
})
export class UserGroupsHeaderComponent {
  @Input() public group: any;
  isLoading: boolean = false;
  isBusy: boolean = false;
  errorMsg: string | null = null;
  users: any[] = []; // Array to hold the list of users
  assignedUsers: any[] = []; // Array to hold the list of assigned users
  data: any = {}; // Object to hold data for API requests

  @ViewChild('searchInput', { static: false })
  searchInput!: ElementRef<HTMLInputElement>;

  constructor(
    private findService: FindService,
  private LocalStorageService: LocalStorageService
  ) {}

  reloadUsers() {
    console.log('reloadUsers');
    let library = localStorage.getItem('library');
    let dt = { library: library, group: this.group.id_gr };
    this.findService.api_post('admin/group/assignGroup', dt).subscribe({
      next: (data) => {
        this.assignedUsers = data;
        console.log('---------------', data);
      },
      error: (err) => {
        this.errorMsg = 'Não foi possível obter a lista de usuários.';
        this.isLoading = false;
        this.isBusy = false;
      },
    });
  }

  search() {
    const inputEl = this.searchInput.nativeElement;
    let nameSearch = inputEl.value;
    if (this.isBusy) {
      return;
    } else {
      console.log(nameSearch);
      this.isBusy = true;
      let dt = { q: nameSearch };
      this.findService.api_post('users/search', dt).subscribe({
        next: (data) => {
          console.log(data);
          this.users = data;
          this.isLoading = false;
          this.isBusy = false;
        },
        error: (err) => {
          this.errorMsg = 'Não foi possível obter a lista de usuários.';
          this.isLoading = false;
          this.isBusy = false;
        },
      });
    }
  }

  addUserToGroup(idUser: string) {

    let dt = {
      id_us: idUser,
      id_gr: this.group.id_gr
      }

    this.findService.api_post('admin/group/addToGroup', dt)
      .subscribe({
      next: (res) => {
        this.data = res;
        console.log(res);
        this.reloadUsers();
      },
      error: (err) => {
        this.errorMsg = '' + err.error.message;
      }})
  }

  ngAfterViewInit(): void {
    // Só depois que o template existir, registramos o listener
    if (this.searchInput) {
      this.searchInput.nativeElement.addEventListener('keyup', () =>
        this.search()
      );
    }
  }
}
