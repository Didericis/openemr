# Appendix: Pre-Search Checklist

Use this list to ensure you've thought through a variety of perspectives in your
planning.

## Phase 1: Define Your Constraints

### 1. Domain Selection

- What specific use cases will you support?
    - Doctor expecting a user coming in for a specific appointment
    - We'll start with low risk doctor profile so mistakes do not cost us as
      much as they might otherwise
    -
- What are the verification requirements for this domain?
    - Probably depends on the specific persona/user... tricky one
- What data sources will you need access to?
    - Should probably assume everything is in the EMR, but would likely need to
      supplement what gets added in a real project
    -

### 2. Scale & Performance

- Expected query volume?
    - Probably will differ per tenant and role
- Acceptable latency for responses?
    - This will differ per role. Maybe could have some sort of a queue where
      certain roles/users are prioritized.
- Concurrent user requirements?
    - Will also vary substantially per tenant and use case
- Cost constraints for LLM calls?
    - Want to minimize costs for all precompute/vectorizing steps

### 3. Reliability Requirements

- What's the cost of a wrong answer in your domain?
    - Want to quantify the cost if possible/make it explicit
- What verification is non-negotiable?
- Human-in-the-loop requirements?
    - Want a human in the loop to verify the patient is who they say
- Audit/compliance needs?

### 4. Team & Skill Constraints

- Familiarity with agent frameworks?
- Experience with your chosen domain?
- Comfort with eval/testing frameworks?

## Phase 2: Architecture Discovery

### 5. Agent Framework Selection

- Single agent or multi-agent architecture?
- State management requirements?
- Tool integration complexity?

### 6. LLM Selection

- OpenAI vs Claude vs open source?
- Structured output support requirements?
- Context window needs?
- Cost per query acceptable?

### 7. Tool Design

- What tools does your agent need?
- External API dependencies?
- Mock vs real data for development?
- Error handling per tool?

### 8. Observability Strategy

- LangSmith vs Langfuse vs Braintrust vs other?
- What metrics matter most?
- Real-time monitoring needs?
- Cost tracking requirements?

### 9. Eval Approach

- How will you measure correctness?
- Ground truth data sources?
- Automated vs human evaluation?
- CI integration for eval runs?

### 10. Verification Design

- What claims must be verified?
- Fact-checking data sources?
- Confidence thresholds?
- Escalation triggers?

## Phase 3: Post-Stack Refinement

### 11. Failure Mode Analysis

- What happens when tools fail?
- How to handle ambiguous queries?
- Rate limiting and fallback strategies?
- Graceful degradation approach?

### 12. Security Considerations

- Prompt injection prevention?
- Data leakage risks?
- API key management?
- Audit logging requirements?

### 13. Testing Strategy

- Unit tests for tools?
- Integration tests for agent flows?
- Adversarial testing approach?
- Regression testing setup?

### 14. Open Source Planning

- What will you release?
- Licensing considerations?
- Documentation requirements?
- Community engagement plan?

### 15. Deployment & Operations

- Hosting approach?
- CI/CD for agent updates?
- Monitoring and alerting?
- Rollback strategy?

### 16. Iteration Planning

- How will you collect user feedback?
- Eval-driven improvement cycle?
- Feature prioritization approach?
- Long-term maintenance plan?
