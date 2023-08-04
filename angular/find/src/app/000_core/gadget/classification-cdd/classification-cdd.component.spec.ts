import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ClassificationCddComponent } from './classification-cdd.component';

describe('ClassificationCddComponent', () => {
  let component: ClassificationCddComponent;
  let fixture: ComponentFixture<ClassificationCddComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ClassificationCddComponent]
    });
    fixture = TestBed.createComponent(ClassificationCddComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
