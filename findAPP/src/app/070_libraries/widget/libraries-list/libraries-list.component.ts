import { Component } from '@angular/core';
import { FindService } from '../../../010_core/service/find.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-libraries-list',
  standalone: false,
  templateUrl: './libraries-list.component.html',
  styleUrl: './libraries-list.component.scss',
})
export class LibrariesListComponent {
  data: Array<any> | any;
  libraries: Array<any> | any;
  defaultLogo = 'assets/placeholder-logo.svg';

  constructor(
    private findService: FindService, // private router: Router
    private router: Router
  ) {}

  editLibrary(library: any) {
    //this.router.navigate(['/libraries', library.id, 'edit']);
  }

  createLibrary() {
    this.router.navigate(['/libraries/create']);
  }

  ngOnInit() {
    console.log('OKK');
    this.findService.api_post('library', []).subscribe((res) => {
      console.log(res);
      this.data = res;
      this.libraries = this.data.library;
    });
  }
}
