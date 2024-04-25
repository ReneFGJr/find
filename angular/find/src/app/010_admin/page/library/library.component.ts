import { Router } from '@angular/router';
import { FindService } from './../../../000_core/service/find.service';
import { Component } from '@angular/core';

@Component({
  selector: 'app-library',
  templateUrl: './library.component.html',
})
export class LibraryComponent {
  public libraries: Array<any> | any;

  constructor(private findService: FindService, private router: Router) {}

  ngOnInit() {
    this.findService.api_post('library', []).subscribe((res) => {
      this.libraries = res;
      console.log(res);
    });
  }

  selectLibrary(ID: string) {
    this.router.navigate(['/', 'admin','library',ID]);
  }
}
