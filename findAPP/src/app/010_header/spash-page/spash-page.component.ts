import { Component } from '@angular/core';

@Component({
  selector: 'app-spash-page',
  standalone: false,
  templateUrl: './spash-page.component.html',
  styleUrl: './spash-page.component.scss',
})
export class SpashPageComponent {
  showNavbar = false;
  logo = 'assets/logo_find_big_bw.png';

  ngOnInit(): void {
    setTimeout(() => {
      this.showNavbar = true;
    }, 2000);
  }
}
