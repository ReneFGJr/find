import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AcervoComponent } from './acervo.component';

describe('AcervoComponent', () => {
  let component: AcervoComponent;
  let fixture: ComponentFixture<AcervoComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [AcervoComponent]
    });
    fixture = TestBed.createComponent(AcervoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
