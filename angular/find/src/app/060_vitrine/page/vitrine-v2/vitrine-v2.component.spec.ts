import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VitrineV2Component } from './vitrine-v2.component';

describe('VitrineV2Component', () => {
  let component: VitrineV2Component;
  let fixture: ComponentFixture<VitrineV2Component>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [VitrineV2Component]
    });
    fixture = TestBed.createComponent(VitrineV2Component);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
