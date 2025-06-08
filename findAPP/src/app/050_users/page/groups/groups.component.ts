import { Component } from '@angular/core';
import { ActivatedRoute, ParamMap } from '@angular/router';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-groups',
  standalone: false,
  templateUrl: './groups.component.html',
  styleUrl: './groups.component.scss',
})
export class GroupsComponent {
  public act: string = 'list';
  public idGroup: string = '';
  public loading: boolean = false;
  private routeSub!: Subscription;

  constructor(private route: ActivatedRoute) {}

  ngOnInit() {
    // Assina o paramMap para reagir sempre que a rota (':act' ou ':id') mudar
    this.routeSub = this.route.paramMap.subscribe((params: ParamMap) => {
      this.act = params.get('act') ?? 'list';
      this.idGroup = params.get('id') ?? '';
    });
  }
}
