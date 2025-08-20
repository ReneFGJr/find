import { Component } from '@angular/core';
import { ActivatedRoute, ParamMap } from '@angular/router';
import { SocialService } from '../../../010_core/service/social.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-reposrts',
  standalone: false,
  templateUrl: './reports.component.html',
  styleUrl: './reports.component.scss'
})
export class ReportsComponent {
  public act: string = 'list';
  public userID = 0;
  public loading: boolean = false;

  private routeSub!: Subscription;

  constructor(private route: ActivatedRoute, private socialService: SocialService) {}

  ngOnInit() {
    // Assina o paramMap para reagir sempre que a rota (':act' ou ':id') mudar
    this.routeSub = this.route.paramMap.subscribe((params: ParamMap) => {
      this.act = params.get('act') ?? 'list';
      this.userID = Number(params.get('id')) || 0;

      if (this.act === 'logout') {
        this.socialService.logout();
      }
    });
  }
}
