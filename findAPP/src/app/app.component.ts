import { Component } from '@angular/core';
import { SocialService } from './010_core/service/social.service';
import { NavigationEnd, Router } from '@angular/router';
import { filter } from 'rxjs/operators';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  standalone: false,
  styleUrl: './app.component.scss',
})
export class AppComponent {
  title = 'findAPP';
  isLoged: boolean = false;
  user: any;

  constructor(private socialService: SocialService, private router: Router) {}

  ngOnInit() {
    // Atualiza ao iniciar
    this.updateLoginFlag();

    // Sempre que terminar uma navegação, atualiza
    this.router.events
      .pipe(filter(evt => evt instanceof NavigationEnd))
      .subscribe(() => {
        this.updateLoginFlag();
      });
  }

  private updateLoginFlag() {
    this.isLoged = this.socialService.isLoggedIn();
    this.user = this.socialService.getUser();
  }
}
